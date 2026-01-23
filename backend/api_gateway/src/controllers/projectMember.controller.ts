import type { Request, Response } from "express";
import type {
  ProjectMembers,
  ProjectMemberParams,
  ProjectParams,
} from "../Types/types.js";
import AppError from "../services/appError.service.js";
import { getHeaders, resolveResponse } from "../utils/helpers.js";

const post = async (req: Request<ProjectParams>, res: Response) => {
  const token = req.headers.authorization!;
  const id = Number.parseInt(req.params.id);
  const request = req.body as ProjectMembers;

  if (Number.isNaN(id)) throw new AppError(400, "Invalid project ID.");

  const response = await fetch(
    `${process.env.APP_URL}/projects/${id}/members`,
    {
      headers: getHeaders({ authorization: token }),
      method: "post",
      body: JSON.stringify(request),
    },
  );

  return await resolveResponse(response, res);
};

const destroy = async (req: Request<ProjectMemberParams>, res: Response) => {
  const token = req.headers.authorization!;
  const id = Number.parseInt(req.params.id);
  const memberId = Number.parseInt(req.params.memberId);

  if (Number.isNaN(id) || Number.isNaN(memberId))
    throw new AppError(400, "Invalid ID.");

  const response = await fetch(
    `${process.env.APP_URL}/projects/${id}/members/${memberId}`,
    {
      headers: getHeaders({ authorization: token }),
      method: "delete",
    },
  );

  return await resolveResponse(response, res);
};

export default { post, destroy };
