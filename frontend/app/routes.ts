import {
  index,
  prefix,
  route,
  type RouteConfig,
} from "@react-router/dev/routes";

export default [
  index("views/auth/Register.tsx"),

  route("login", "views/auth/Login.tsx"),
  route("test", "views/TestPage.tsx"),

  ...prefix("project", [
    route("", "views/project/Index.tsx"),
    route("create", "views/project/Create.tsx"),
  ]),
] satisfies RouteConfig;
