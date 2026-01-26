import { Router } from "express";
import projectController from "../controllers/project.controller.js";
import tokenHandler from "../middlewares/tokenHandler.middleware.js";

const projectsRoutes = Router();

projectsRoutes.use(tokenHandler);

projectsRoutes.get("/projects", projectController.index);

projectsRoutes.post("/projects", projectController.store);

projectsRoutes.get("/projects/:id", projectController.show);

projectsRoutes.put("/projects/:id", projectController.update);

projectsRoutes.delete("/projects/:id", projectController.destroy);

export default projectsRoutes;
