import logo from './text-editor.png';
import './App.css';
import React, { useState, useEffect } from 'react'
import {auth, google, twitter, github} from './config/fire'
import {signInWithPopup, signOut} from 'firebase/auth' 

function App() {

  const [isLogin, setIsLogin] = useState(false)
  const [user, setUser] = useState(null)
  
  useEffect(() => {
    localStorage.setItem("userid", user.tenantId);
  }, [user]);
  
  const LoginFalse = () => (
    <>
    <div className='topbox'>
    <svg style={{borderRadius:"10px"}} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#5000ca" fillOpacity="1" d="M0,256L40,245.3C80,235,160,213,240,208C320,203,400,213,480,192C560,171,640,117,720,96C800,75,880,85,960,128C1040,171,1120,245,1200,261.3C1280,277,1360,235,1400,213.3L1440,192L1440,0L1400,0C1360,0,1280,0,1200,0C1120,0,1040,0,960,0C880,0,800,0,720,0C640,0,560,0,480,0C400,0,320,0,240,0C160,0,80,0,40,0L0,0Z"></path></svg>
      <div style={{padding:"50px"}}>
      <div style={{}}>
        <img style={{width:"150px", backgroundColor:"#eee",padding:"30px",borderRadius:"50px",border:"2px solid black", margin: "auto"}} src={logo} alt='image' />
        <br/>
        <h4 style={{color:"black"}}>Login to Save your Paste</h4>
      </div>
      <div style={{display:"flex", justifyContent:"center"}}>
      <div><button className='loginbuttons' style={{ backgroundColor:'#de5246', color:'white',padding:"15px",margin:"10px",borderRadius:"15px",fontFamily:"sans-serif",fontSize:"15px",cursor:"pointer", height:"50px", width: "50px"}}
      onClick={() => login(google)}>
      <i className="fa fa-google fa-lg"></i>
      </button></div>
      <div><button className='loginbuttons' style={{ backgroundColor:'#00acee', color:'white',padding:"15px",margin:"10px",borderRadius:"15px",fontFamily:"sans-serif",fontSize:"15px",cursor:"pointer", height:"50px", width: "50px"}}
      onClick={() => login(twitter)}>
      <i className="fa fa-twitter fa-lg"></i>
      </button></div>
      <div><button className='loginbuttons' style={{backgroundColor:'black', color:'white',padding:"15px",margin:"10px",borderRadius:"15px",fontFamily:"sans-serif",fontSize:"15px",cursor:"pointer", height:"50px", width: "50px"}}
      onClick={() => {login(github)}}>
      <i className="fa fa-github fa-lg"></i>
      </button></div>

    </div>
    </div>
    <svg style={{borderRadius:"10px"}} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#5000ca" fillOpacity="1" d="M0,224L48,192C96,160,192,96,288,101.3C384,107,480,181,576,181.3C672,181,768,107,864,112C960,117,1056,203,1152,224C1248,245,1344,203,1392,181.3L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>


    </div>

    </>
  )
  
  const LoginTrue = () => (
    <>
      <h1>Welcome!</h1>
      <img src={user.photoURL} style={{width:120}}/>
      <p>Welcome {user.displayName}! Your account {user.email} has been successfully logged in at {user.metadata.lastSignInTime}</p>
      <button style={{width:150}} onClick={logout}>
        Logout
      </button>
    </>
  )
  
  const login = async(provider) => {
    const result = await signInWithPopup(auth, provider) 
    setUser(result.user)
    setIsLogin(true)
    console.log(result)
  }

  const logout = async() => {
    const result = await signOut(auth)
    setUser(null)
    setIsLogin(false)
    console.log(result)
  }
  
  return (
    <div className="App">
      <header className="App-header">
        
      {isLogin && user ? <LoginTrue/> : <LoginFalse/>}

      </header>
    </div>
  );
}

export default App;