import { Router } from "express";
import profileController from "../controllers/profile.controller.js";

const profileRoutes = Router();

profileRoutes.get("/profile", profileController.show);

profileRoutes.put("/profile", profileController.update);

export default profileRoutes;
