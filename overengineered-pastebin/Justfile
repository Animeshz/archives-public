run:
  docker start redis-stack || docker run -d --name redis-stack -p 6379:6379 -p 8001:8001 -v $HOME/redis-data:/data redis/redis-stack:latest
  cd frontend && pnpm i && pnpm dev &
  cd backend && cargo run
  wait

