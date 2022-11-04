use axum::http::Method;
use axum::{extract::Query, response::Html, routing::*, Extension, Router};
use nanoid::nanoid;
use redis::JsonAsyncCommands;
use redis::AsyncCommands;
use redis::{aio::Connection, RedisError};
use serde_json::json;
use serde_json::Value;
use std::{borrow::BorrowMut, collections::HashMap, net::SocketAddr, sync::Arc};
use tokio::sync::Mutex;
use tower_http::cors::{Any, CorsLayer};

async fn redis_instance() -> redis::RedisResult<Connection> {
    let client = redis::Client::open("redis://127.0.0.1/")?;
    client.get_async_connection().await
}

#[tokio::main]
async fn main() {
    let mut redis = redis_instance().await.unwrap();

    let exist: Result<String, _> = redis.json_get("pastes", "$").await;
    if let Err(_) = exist {
        let _: Result<(), _> = redis.json_set("pastes", "$", &json!([])).await;
    }

    let cors = CorsLayer::new()
    .allow_methods([Method::GET, Method::POST, Method::PUT, Method::DELETE])
    .allow_origin(Any);

    // build our application with a route
    let app = Router::new()
        .route("/", get(handler))
        .route("/paste", get(get_paste))
        .route("/paste", post(new_paste))
        .route("/paste", put(update_paste))
        .route("/paste", delete(delete_paste))
        .route("/user_pastes", get(get_all_pastes_for_user))
        .layer(Extension(Arc::new(Mutex::new(redis))))
        .layer(cors);

    // run it
    let addr = SocketAddr::from(([127, 0, 0, 1], 4000));
    println!("listening on {}", addr);
    axum::Server::bind(&addr)
        .serve(app.into_make_service())
        .await
        .unwrap();
}

async fn handler() -> Html<&'static str> {
    Html("<h1>Hello, World!</h1>")
}

async fn new_paste(
    Extension(redis): Extension<Arc<Mutex<Connection>>>,
    Query(params): Query<HashMap<String, String>>,
    body: String,
) -> Result<String, String> {
    let paste_id = nanoid!();
    let user_id = params.get("user_id");

    let mut redis = redis.lock().await;

    redis
        .json_arr_append(
            "pastes",
            "$",
            &json!(
                {
                    "id": &paste_id,
                    "user_id": user_id,
                    "contents": [body],
                }
            ),
        )
        .await
        .map_err(|e| format!("{:#?}", e))?;

    Ok(paste_id)
}

async fn get_paste(
    Extension(redis): Extension<Arc<Mutex<Connection>>>,
    Query(params): Query<HashMap<String, String>>,
) -> Result<String, String> {
    let paste_id = params.get("paste_id").ok_or_else(|| "Expected a paste_id")?;
    let mut redis = redis.lock().await;

    redis
        .json_get(
            "pastes",
            format!("$[?(@.id==\"{}\")]", paste_id.trim()),
        )
        .await
        .map_err(|e| format!("{:#?}", e))
}

async fn update_paste(
    Extension(redis): Extension<Arc<Mutex<Connection>>>,
    Query(params): Query<HashMap<String, String>>,
    body: String,
) -> Result<(), String> {
    let paste_id = params.get("paste_id").ok_or_else(|| "Expected a paste_id")?;
    let mut redis = redis.lock().await;

    redis
        .json_arr_append(
            "pastes",
            format!("$[?(@.id==\"{}\")].contents", paste_id.trim()),
            &json!(body)
        )
        .await
        .map_err(|e| format!("{:#?}", e))?;

    Ok(())
}

async fn delete_paste(
    Extension(redis): Extension<Arc<Mutex<Connection>>>,
    Query(params): Query<HashMap<String, String>>,
) -> Result<(), String> {
    let paste_id = params.get("paste_id").ok_or_else(|| "Expected a paste_id")?;
    let mut redis = redis.lock().await;

    redis
        .json_del(
            "pastes",
            format!("$[?(@.id==\"{}\")]", paste_id.trim()),
        )
        .await
        .map_err(|e| format!("{:#?}", e))?;

    Ok(())
}

async fn get_all_pastes_for_user(
    Extension(redis): Extension<Arc<Mutex<Connection>>>,
    Query(params): Query<HashMap<String, String>>,
) -> Result<String, String> {
    let user_id = params.get("user_id").ok_or_else(|| "Expected a user_id")?;
    let mut redis = redis.lock().await;

    redis
        .json_get(
            "pastes",
            format!("$[?(@.user_id==\"{}\")]", user_id.trim()),
        )
        .await
        .map_err(|e| format!("{:#?}", e))
}
