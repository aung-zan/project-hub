import type { Request, Response } from "express";

import { getHeaders, resolveResponse } from "../utils/helpers.js";
import type {
  ProjectMembers,
  ProjectMemberParams,
  ProjectParams,
} from "../Types/types.js";

const post = async (req: Request<ProjectParams>, res: Response) => {
  const token = req.headers.authorization!;
  const id = req.params.id;
  const request = req.body as ProjectMembers;

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
  const id = req.params.id;
  const memberId = req.params.memberId;

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
