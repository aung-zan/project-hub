import express from "express";
import { createServer } from "node:http";
import cors from "cors";
import cookieParser from "cookie-parser";

import routes from "./routes/routes.js";
import errorHandler from "./middlewares/errorHandler.middleware.js";
import limiter from "./middlewares/rateLimiter.middleware.js";
import { getCorsOptions } from "./utils/helpers.js";

const app = express();

app.use(express.json());
app.use(cors(getCorsOptions));
app.use(cookieParser());
app.use(limiter);

app.use("/api", routes);
app.use(errorHandler);

const server = createServer(app);
const port = process.env.SERVER_PORT;

server.listen(port, () => {
  console.log(`server is running on http://localhost:${port}`);
});
