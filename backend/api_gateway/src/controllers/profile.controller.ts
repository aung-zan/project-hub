import type { Request, Response } from "express";
import type { UserUpdate } from "../Types/types.js";
import { getHeaders, resolveResponse } from "../utils/helpers.js";

const show = async (req: Request, res: Response) => {
  const token = req.headers.authorization!;

  const response = await fetch(`${process.env.APP_URL}/profile`, {
    headers: getHeaders({ authorization: token }),
  });

  return await resolveResponse(response, res);
};

const update = async (req: Request, res: Response) => {
  const token = req.headers.authorization!;
  const request = req.body as UserUpdate;

  const response = await fetch(`${process.env.APP_URL}/profile`, {
    headers: getHeaders({ authorization: token }),
    method: "put",
    body: JSON.stringify(request),
  });

  return await resolveResponse(response, res);
};

export default { show, update };
