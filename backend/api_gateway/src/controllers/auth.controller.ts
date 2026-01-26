import type { Request, Response } from "express";

import type { UserCreate, UserLogin } from "../Types/types.js";
import { getHeaders, resolveResponse } from "../utils/helpers.js";

const register = async (req: Request, res: Response) => {
  const request = req.body as UserCreate;

  const response = await fetch(`${process.env.APP_URL}/register`, {
    headers: getHeaders({}),
    method: "POST",
    body: JSON.stringify(request),
  });

  return await resolveResponse(response, res);
};

const login = async (req: Request, res: Response) => {
  const request = req.body as UserLogin;

  const response = await fetch(`${process.env.APP_URL}/login`, {
    headers: getHeaders({}),
    method: "POST",
    body: JSON.stringify(request),
  });

  return await resolveResponse(response, res);
};

const logout = async (req: Request, res: Response) => {};

export default { register, login, logout };
