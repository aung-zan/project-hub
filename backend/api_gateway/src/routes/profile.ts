import { Router } from "express";
import profileController from "../controllers/profile.controller.js";
import tokenHandler from "../middlewares/tokenHandler.middleware.js";

const profileRoutes = Router();

profileRoutes.use(tokenHandler);

profileRoutes.get("/profile", profileController.show);

profileRoutes.put("/profile", profileController.update);

export default profileRoutes;
