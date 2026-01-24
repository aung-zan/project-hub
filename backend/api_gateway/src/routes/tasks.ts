import { Router } from "express";
import tokenHandler from "../middlewares/tokenHandler.middleware.js";
import taskController from "../controllers/task.controller.js";

const tasksRoutes = Router();

tasksRoutes.use(tokenHandler);

tasksRoutes.get("/projects/:id/tasks", taskController.index);

tasksRoutes.post("/projects/:id/tasks", taskController.store);

tasksRoutes.get("/tasks/:id", taskController.show);

tasksRoutes.put("/tasks/:id", taskController.update);

tasksRoutes.delete("/tasks/:id", taskController.destroy);

export default tasksRoutes;
