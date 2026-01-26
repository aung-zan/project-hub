import { Router } from "express";

import tokenHandler from "../middlewares/tokenHandler.middleware.js";
import projectMemberController from "../controllers/projectMember.controller.js";

const projectMembersRoutes = Router();

projectMembersRoutes.use(tokenHandler);

projectMembersRoutes.post(
  "/projects/:id/members",
  projectMemberController.post,
);

projectMembersRoutes.delete(
  "/projects/:id/members/:memberId",
  projectMemberController.destroy,
);

export default projectMembersRoutes;
