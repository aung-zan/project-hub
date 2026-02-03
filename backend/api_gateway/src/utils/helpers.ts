import type { Response } from "express";
import type { ResponseT } from "../Types/types.js";
import AppError from "../services/appError.service.js";
import type { CorsOptions } from "cors";

export const getCorsOptions = () => {
  return {
    origin: ["http://localhost:5173"],
    credentials: true,
  } as CorsOptions;
};

export const getHeaders = (header: {}) => {
  return {
    "content-type": "application/json",
    accept: "application/json",
    ...header,
  };
};

export const resolveResponse = async (
  response: globalThis.Response,
  res: Response,
  cookie?: boolean,
) => {
  const status = response.status;
  const data = (await response.json()) as ResponseT;

  if (data.success === false)
    throw new AppError(status, data.message || "Something went wrong.");

  if (cookie && cookie === true) {
    const token = data.data?.access_token;

    return res
      .cookie("access_token", token, {
        httpOnly: true,
        // secure: true,
        sameSite: "lax",
        maxAge: 3600000,
      })
      .status(200)
      .json(data);
  }

  return res.status(200).json(data);
};
