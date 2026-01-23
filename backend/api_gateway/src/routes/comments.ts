import { Router } from "express";
import tokenHandler from "../middlewares/tokenHandler.middleware.js";
import commentController from "../controllers/comment.controller.js";

const commentsRoutes = Router();

commentsRoutes.use(tokenHandler);

commentsRoutes.post("/tasks/:id/comments", commentController.store);

commentsRoutes.put("/tasks/:id/comments/:commentId", commentController.update);

commentsRoutes.delete(
  "/tasks/:id/comments/:commentId",
  commentController.destroy,
);

export default commentsRoutes;
