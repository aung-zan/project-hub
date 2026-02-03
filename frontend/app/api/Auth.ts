import type { Response, UserAuth, UserCreate } from "app/types";
import { getHeaders } from "app/utils/helper";

export const register = async (data: UserCreate): Promise<Response> => {
  const response = await fetch(`${import.meta.env.VITE_APP_URL}/register`, {
    headers: getHeaders({}),
    method: "post",
    body: JSON.stringify(data),
  });

  return (await response.json()) as Response;
};

export const login = async (data: UserAuth): Promise<Response> => {
  const response = await fetch(`${import.meta.env.VITE_APP_URL}/login`, {
    headers: getHeaders({}),
    method: "post",
    credentials: "include",
    body: JSON.stringify(data),
  });

  return (await response.json()) as Response;
};
