"use server";

import { cookies } from "next/headers";
import { jwtVerify, SignJWT } from "jose";
import { NextRequest, NextResponse } from "next/server";
import axios from "axios";
import { getTokenExpiration } from "./utils";

const secretKey = process.env.SECRET_KEY;
const key = new TextEncoder().encode(secretKey);

export async function encrypt_key(item) {
  return await new SignJWT(item)
    .setProtectedHeader({ alg: "HS256" }) // SET THE PROTECTED HEADER
    .setIssuedAt() // ISSUED TIME
    .setExpirationTime("1d") // EXPIRATION TIME
    .sign(key); // SIGN THE KEY
}

export async function decrypt_key(data) {
  const { payload } = await jwtVerify(data, key, {
    algorithms: ["HS256"],
  });

  return payload;
}

axios.defaults.headers.post["Content-Type"] = "application/json";

//HAHAHAHA WTF
const AUTH_ENDPOINT = `${process.env.NEXT_PUBLIC_API_BASE_URL}auth/login`;
const SIGNUP_ENDPOINT = `${process.env.NEXT_PUBLIC_API_BASE_URL}auth/signup`;
const USERNAME_AVAILABILITY = `${process.env.NEXT_PUBLIC_API_BASE_URL}auth/check_username`;

export async function login(username, password) {
  try {
    const res = await axios
      .post(AUTH_ENDPOINT, {
        user: username,
        password: password,
      })
      .then(async (response) => {
        if (response.data.success === false) {
          return { success: false, message: response.data.message };
        }
        const token = response.data.data.token;
        const expiration = new Date(
          getTokenExpiration(response.data.data.token) * 1000
        );

        cookies().set("session", token, {
          expires: expiration,
          httpOnly: true,
          secure: process.env.NODE_ENV === "production",
          sameSite: "Strict",
        });

        return { success: true, message: response.data.data.message };
      })
      .catch((err) => {
        return { success: false, message: err.message };
      });

    return res;
  } catch (err) {
    return { success: false, message: err };
  }
}

export async function signup(
  firstname,
  lastname,
  username,
  email,
  password,
  dob,
  pnum,
  gender
) {
  const res = await axios
    .post(SIGNUP_ENDPOINT, {
      firstname: firstname,
      lastname: lastname,
      username: username,
      email: email,
      password: password,
      dob: dob,
      phone_num: pnum,
      gender: gender,
    })
    .then((response) => {
      if (response.data.success === false) {
        return { success: false, message: response.data.message };
      }
      return { success: true, message: response.data.message };
    })
    .catch((err) => {
      if (err.response) {
        return { success: false, message: err.response.data.message };
      }
      return {
        success: false,
        message: err.message || "An unknown error occurred",
      };
    });

  return res;
}

export async function checkUsernameAvailability(username) {
  const res = await axios
    .post(USERNAME_AVAILABILITY, {
      user: username,
    })
    .then((response) => {
      if (response.data.data === false) {
        return { success: true, message: response.data.data };
      } else {
        return { success: false, message: response.data.data };
      }
    })
    .catch((err) => {
      return { success: false, message: err.message };
    });

  return res;
}

export async function getSession() {
  const session = await cookies().get("session")?.value;

  if (!session) {
    return null;
  }

  return session;
}
