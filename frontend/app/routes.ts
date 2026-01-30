import { index, route, type RouteConfig } from "@react-router/dev/routes";

export default [
  index("views/auth/Register.tsx"),
  route("test", "views/TestPage.tsx"),
  route("login", "views/auth/Login.tsx"),
] satisfies RouteConfig;
