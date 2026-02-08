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
    route("", "views/project/Index.tsx", { id: "project.index" }),
    route("create", "views/project/Create.tsx", { id: "project.create" }),
  ]),

  ...prefix("task", [
    route("", "views/task/Index.tsx", { id: "task.index" }),
    route("create", "views/task/Create.tsx", { id: "task.create" }),
  ]),
] satisfies RouteConfig;
