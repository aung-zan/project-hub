import type { Request, Response } from "express";
import AppError from "../services/appError.service.js";

const test = async (req: Request, res: Response) => {
  const response = await fetch("http://localhost/api/test");

  const data = await response.json();

  if (data.success === false) {
    throw new AppError(402, "Bad Data.");
  }

  return res.status(200).json({
    success: true,
    data,
  });
};

export default { test };
