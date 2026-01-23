import type { Request, Response } from "express";

import { getHeaders, resolveResponse } from "../utils/helpers.js";
import type {
  ProjectCreate,
  ProjectParams,
  ProjectUpdate,
} from "../Types/types.js";
import AppError from "../services/appError.service.js";

const index = async (req: Request, res: Response) => {
  const token = req.headers.authorization!;

  const response = await fetch(`${process.env.APP_URL}/projects`, {
    headers: getHeaders({ authorization: token }),
  });

  return await resolveResponse(response, res);
};

const store = async (req: Request, res: Response) => {
  const token = req.headers.authorization!;
  const request = req.body as ProjectCreate;

  const response = await fetch(`${process.env.APP_URL}/projects`, {
    headers: getHeaders({ authorization: token }),
    method: "post",
    body: JSON.stringify(request),
  });

  return await resolveResponse(response, res);
};

const show = async (req: Request<ProjectParams>, res: Response) => {
  const token = req.headers.authorization!;
  const id = Number.parseInt(req.params.id);

  if (Number.isNaN(id)) throw new AppError(400, "Invalid project ID.");

  const response = await fetch(`${process.env.APP_URL}/projects/${id}`, {
    headers: getHeaders({ authorization: token }),
  });

  return await resolveResponse(response, res);
};

const update = async (req: Request<ProjectParams>, res: Response) => {
  const token = req.headers.authorization!;
  const id = Number.parseInt(req.params.id);
  const request = req.body as ProjectUpdate;

  if (Number.isNaN(id)) throw new AppError(400, "Invalid project ID.");

  const response = await fetch(`${process.env.APP_URL}/projects/${id}`, {
    headers: getHeaders({ authorization: token }),
    method: "put",
    body: JSON.stringify(request),
  });

  return await resolveResponse(response, res);
};

const destroy = async (req: Request<ProjectParams>, res: Response) => {
  const token = req.headers.authorization!;
  const id = Number.parseInt(req.params.id);

  if (Number.isNaN(id)) throw new AppError(400, "Invalid project ID.");

  const response = await fetch(`${process.env.APP_URL}/projects/${id}`, {
    headers: getHeaders({ authorization: token }),
    method: "delete",
  });

  return await resolveResponse(response, res);
};

export default { index, store, show, update, destroy };
