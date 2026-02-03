import type { NextFunction, Request, Response } from "express";
import AppError from "../services/appError.service.js";

const tokenHandler = (req: Request, res: Response, next: NextFunction) => {
  const token = req.cookies.access_token;

  if (token === undefined)
    throw new AppError(401, "Token is not provided in cookie.");

  req.headers.authorization = `Bearer ${token}`;

  next();
};

export default tokenHandler;
