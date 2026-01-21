import type { NextFunction, Request, Response } from "express";

import AppError from "../services/appError.service.js";
import type { AppErrorT } from "../Types/types.js";

const errorHandler = (
  err: AppErrorT,
  req: Request,
  res: Response,
  next: NextFunction,
) => {
  if (err instanceof AppError === false) {
    console.log(err);
  }

  const status = err.statusCode || 500;
  const message = err.message || "Something went wrong.";

  return res.status(status).json({
    success: false,
    message,
  });
};

export default errorHandler;
