import authRoutes from "./auth.js";
import profileRoutes from "./profile.js";
import projectMembersRoutes from "./projectMembers.js";
import projectsRoutes from "./projects.js";
import testRoutes from "./test.js";

const routes = [
  testRoutes,
  authRoutes,
  profileRoutes,
  projectsRoutes,
  projectMembersRoutes,
];

export default routes;
