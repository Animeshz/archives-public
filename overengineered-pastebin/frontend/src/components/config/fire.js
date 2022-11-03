import { initializeApp } from "firebase/app";
import { getAuth, GoogleAuthProvider, FacebookAuthProvider, TwitterAuthProvider, GithubAuthProvider } from 'firebase/auth'

const firebaseConfig = {
  apiKey: "AIzaSyCazRFbVQVeRRwZ4jUfe5DkkBGluJKGItU",
  authDomain: "fir-auth-e6c85.firebaseapp.com",
  projectId: "fir-auth-e6c85",
  storageBucket: "fir-auth-e6c85.appspot.com",
  messagingSenderId: "759774119949",
  appId: "1:759774119949:web:13ca5d94577c06215bad44",
  measurementId: "G-2CTYDZ2BQ0"
};

initializeApp(firebaseConfig);
export const auth = getAuth()
export const google = new GoogleAuthProvider()
export const facebook = new FacebookAuthProvider()
export const twitter = new TwitterAuthProvider()
export const github = new GithubAuthProvider()
