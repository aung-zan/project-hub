import type { Request, Response } from "express";
import type { ResponseT, UserUpdate } from "../Types/types.js";
import AppError from "../services/appError.service.js";
import { getHeaders } from "../utils/helpers.js";

const show = async (req: Request, res: Response) => {
  const token = req.headers.authorization;

  if (token === undefined) {
    throw new AppError(401, "Token is not provided in header.");
  }

  const response = await fetch(`${process.env.APP_URL}/profile`, {
    headers: getHeaders({ authorization: token }),
  });

  const status = response.status;
  const data = (await response.json()) as ResponseT;

  if (data.success === false)
    throw new AppError(status, data.message || "Something went wrong.");

  return res.status(200).json(data);
};

const update = async (req: Request, res: Response) => {
  const token = req.headers.authorization;

  if (token === undefined) {
    throw new AppError(401, "Token is not provided in header.");
  }

  const request = req.body as UserUpdate;

  const response = await fetch(`${process.env.APP_URL}/profile`, {
    headers: getHeaders({ authorization: token }),
    method: "put",
    body: JSON.stringify(request),
  });

  const status = response.status;
  const data = (await response.json()) as ResponseT;

  if (data.success === false)
    throw new AppError(status, data.message || "Something went wrong.");

  return res.status(200).json(data);
};

export default { show, update };
