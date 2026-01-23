import { Router } from "express";
import tokenHandler from "../middlewares/tokenHandler.middleware.js";
import taskController from "../controllers/task.controller.js";

const tasksRoutes = Router();

tasksRoutes.use(tokenHandler);

tasksRoutes.get("/projects/:id/tasks", taskController.index);

tasksRoutes.post("/projects/:id/tasks", taskController.store);

tasksRoutes.get("/projects/:id/tasks/taskId", taskController.show);

tasksRoutes.put("/projects/:id/tasks/taskId", taskController.update);

tasksRoutes.delete("/projects/:id/tasks/taskId", taskController.destroy);

export default tasksRoutes;
