import type { Request, Response } from "express";

import AppError from "../services/appError.service.js";
import { getHeaders, resolveResponse } from "../utils/helpers.js";
import type {
  CommentStore,
  CommentStoreParams,
  CommentUpdate,
  CommentUpdateParams,
} from "../Types/types.js";

const store = async (req: Request<CommentStoreParams>, res: Response) => {
  const token = req.headers.authorization!;
  const id = Number.parseInt(req.params.id);
  const request = req.body as CommentStore;

  if (Number.isNaN(id)) throw new AppError(400, "Invalid task ID.");

  const response = await fetch(`${process.env.APP_URL}/tasks/${id}/comments`, {
    headers: getHeaders({ authorization: token }),
    method: "post",
    body: JSON.stringify(request),
  });

  return await resolveResponse(response, res);
};

const update = async (req: Request<CommentUpdateParams>, res: Response) => {
  const token = req.headers.authorization!;
  const id = Number.parseInt(req.params.id);
  const commentId = Number.parseInt(req.params.commentId);
  const request = req.body as CommentUpdate;

  if (Number.isNaN(id) || Number.isNaN(commentId))
    throw new AppError(400, "Invalid ID.");

  const response = await fetch(
    `${process.env.APP_URL}/tasks/${id}/comments/${commentId}`,
    {
      headers: getHeaders({ authorization: token }),
      method: "put",
      body: JSON.stringify(request),
    },
  );

  return await resolveResponse(response, res);
};

const destroy = async (req: Request<CommentUpdateParams>, res: Response) => {
  const token = req.headers.authorization!;
  const id = Number.parseInt(req.params.id);
  const commentId = Number.parseInt(req.params.commentId);

  if (Number.isNaN(id) || Number.isNaN(commentId))
    throw new AppError(400, "Invalid ID.");

  const response = await fetch(
    `${process.env.APP_URL}/tasks/${id}/comments/${commentId}`,
    {
      headers: getHeaders({ authorization: token }),
      method: "delete",
    },
  );

  return await resolveResponse(response, res);
};

export default { store, update, destroy };
