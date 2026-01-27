import type { Request, Response } from "express";

import { resolveResponse } from "../utils/helpers.js";

const test = async (req: Request, res: Response) => {
  console.log(process.env.APP_URL);
  const response = await fetch(`${process.env.APP_URL}/test`);

  return await resolveResponse(response, res);
};

export default { test };
