import { Router } from "express";
import testController from "../controllers/test.controller.js";

const testRoutes = Router();

testRoutes.get("/test", testController.test);

export default testRoutes;
