import { Label } from "@/components/ui/label";
import { getRoutesList } from "app/utils/helper";
import { Link, useMatches } from "react-router";

const Sidebar = () => {
  const routesList = getRoutesList();
  const currentRouteId = useMatches()[1].id;
  const currentRoute = currentRouteId.split(".")[0];

  return (
    <div className="flex flex-col h-screen w-64 bg-white border-r border-gray-200 flex-shrink-0">
      <div className="p-6 border-b border-gray-200 h-[73px] flex items-center gap-3">
        <img
          src="../src/assets/hub.svg"
          alt="Project Hub"
          className="w-8 h-8 text-gray-900 fill-gray-900"
        />
        <h1 className="text-xl font-bold text-gray-900">Project Hub</h1>
      </div>

      <nav className="flex-1 py-4 space-y-1 overflow-y-auto">
        {routesList.map((group) => (
          <div key={group.title}>
            <Label className="px-7 mb-2 text-gray-400">{group.title}</Label>

            {group.routes.map((route) => {
              const path = `/${route.id}`;
              const Icon = route.icon;
              const isActive = currentRoute == route.id;

              return (
                <div key={route.id}>
                  <Link to={path}>
                    <button
                      className={`w-full flex items-center gap-3 mb-1 px-7 py-3 border-l-3 cursor-pointer ${
                        isActive
                          ? "border-black bg-gray-200"
                          : "hover:bg-gray-200"
                      }`}
                    >
                      <Icon className="w-5 h-5" />
                      <span className="font-medium">{route.label}</span>
                    </button>
                  </Link>
                </div>
              );
            })}
          </div>
        ))}
      </nav>
    </div>
  );
};

export default Sidebar;
