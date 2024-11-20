import { clsx } from "clsx";
import { twMerge } from "tailwind-merge"

export function cn(...inputs) {
  return twMerge(clsx(inputs));
}

export function getTokenExpiration(token) {
  const payload = token.split('.')[1];
  const decodedPayload = atob(payload);
  const payloadJson = JSON.parse(decodedPayload);
  console.log(payloadJson.exp)
  return payloadJson.exp; 
}

