import type { Response } from "express";
import type { ResponseT } from "../Types/types.js";
import AppError from "../services/appError.service.js";

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
) => {
  const status = response.status;
  const data = (await response.json()) as ResponseT;

  if (data.success === false)
    throw new AppError(status, data.message || "Something went wrong.");

  return res.status(200).json(data);
};
