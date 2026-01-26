import type { Request, Response } from "express";

import { getHeaders, resolveResponse } from "../utils/helpers.js";
import type {
  CommentParams,
  CommentStore,
  CommentUpdate,
} from "../Types/types.js";

const store = async (req: Request<CommentParams>, res: Response) => {
  const token = req.headers.authorization!;
  const id = req.params.id;
  const request = req.body as CommentStore;

  const response = await fetch(`${process.env.APP_URL}/tasks/${id}/comments`, {
    headers: getHeaders({ authorization: token }),
    method: "post",
    body: JSON.stringify(request),
  });

  return await resolveResponse(response, res);
};

const update = async (req: Request<CommentParams>, res: Response) => {
  const token = req.headers.authorization!;
  const id = req.params.id;
  const request = req.body as CommentUpdate;

  const response = await fetch(`${process.env.APP_URL}/comments/${id}`, {
    headers: getHeaders({ authorization: token }),
    method: "put",
    body: JSON.stringify(request),
  });

  return await resolveResponse(response, res);
};

const destroy = async (req: Request<CommentParams>, res: Response) => {
  const token = req.headers.authorization!;
  const id = req.params.id;

  const response = await fetch(`${process.env.APP_URL}/comments/${id}`, {
    headers: getHeaders({ authorization: token }),
    method: "delete",
  });

  return await resolveResponse(response, res);
};

export default { store, update, destroy };
