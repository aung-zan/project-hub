import type { Request, Response } from "express";
import type { ResponseT, UserCreate, UserLogin } from "../Types/types.js";
import AppError from "../services/appError.service.js";
import { getHeaders } from "../utils/helpers.js";

const register = async (req: Request, res: Response) => {
  const request = req.body as UserCreate;

  const response = await fetch(`${process.env.APP_URL}/register`, {
    headers: getHeaders({}),
    method: "POST",
    body: JSON.stringify(request),
  });

  const status = response.status;
  const data = (await response.json()) as ResponseT;

  if (data.success === false)
    throw new AppError(status, data.message || "Something went wrong.");

  return res.status(200).json(data);
};

const login = async (req: Request, res: Response) => {
  const request = req.body as UserLogin;

  const response = await fetch(`${process.env.APP_URL}/login`, {
    headers: getHeaders({}),
    method: "POST",
    body: JSON.stringify(request),
  });

  const status = response.status;
  const data = (await response.json()) as ResponseT;

  if (data.success === false)
    throw new AppError(status, data.message || "Something went wrong.");

  return res.status(200).json(data);
};

const logout = async (req: Request, res: Response) => {};

export default { register, login, logout };
