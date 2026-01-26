import type { Request, Response } from "express";

import { getHeaders, resolveResponse } from "../utils/helpers.js";
import type {
  ProjectParams,
  TaskCreate,
  TaskParams,
  TaskUpdate,
} from "../Types/types.js";

const index = async (req: Request<ProjectParams>, res: Response) => {
  const token = req.headers.authorization!;
  const id = req.params.id;

  const response = await fetch(`${process.env.APP_URL}/projects/${id}/tasks`, {
    headers: getHeaders({ authorization: token }),
  });

  return await resolveResponse(response, res);
};

const store = async (req: Request<ProjectParams>, res: Response) => {
  const token = req.headers.authorization!;
  const id = req.params.id;
  const request = req.body as TaskCreate;

  const response = await fetch(`${process.env.APP_URL}/projects/${id}/tasks`, {
    headers: getHeaders({ authorization: token }),
    method: "post",
    body: JSON.stringify(request),
  });

  return await resolveResponse(response, res);
};

const show = async (req: Request<TaskParams>, res: Response) => {
  const token = req.headers.authorization!;
  const id = req.params.id;

  const response = await fetch(`${process.env.APP_URL}/tasks/${id}`, {
    headers: getHeaders({ authorization: token }),
  });

  return await resolveResponse(response, res);
};

const update = async (req: Request<TaskParams>, res: Response) => {
  const token = req.headers.authorization!;
  const id = req.params.id;
  const request = req.body as TaskUpdate;

  const response = await fetch(`${process.env.APP_URL}/tasks/${id}`, {
    headers: getHeaders({ authorization: token }),
    method: "put",
    body: JSON.stringify(request),
  });

  return await resolveResponse(response, res);
};

const destroy = async (req: Request<TaskParams>, res: Response) => {
  const token = req.headers.authorization!;
  const id = req.params.id;

  const response = await fetch(`${process.env.APP_URL}/tasks/${id}`, {
    headers: getHeaders({ authorization: token }),
    method: "delete",
  });

  return await resolveResponse(response, res);
};

export default { index, store, show, update, destroy };
