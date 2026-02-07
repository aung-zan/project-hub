import { CheckSquare, FolderKanban } from "lucide-react";
import { Link } from "react-router";

const Sidebar = () => {
  return (
    <div className="flex flex-col h-screen w-64 bg-white border-r border-gray-200 flex-shrink-0">
      <div className="p-6 border-b border-gray-200 h-[73px] flex items-center gap-3">
        <img
          src="./src/assets/hub.svg"
          alt="Project Hub"
          className="w-8 h-8 text-gray-900 fill-gray-900"
        />
        <h1 className="text-xl font-bold text-gray-900">Project Hub</h1>
      </div>

      <nav className="flex-1 p-4 space-y-1 overflow-y-auto">
        <Link to="/project">
          <button className="w-full flex items-center gap-3 px-4 py-3 rounded-lg bg-gray-200">
            <FolderKanban className="w-5 h-5" />
            <span className="font-bold">Project</span>
          </button>
        </Link>

        <button className="w-full flex items-center gap-3 px-4 py-3 rounded-lg">
          <CheckSquare className="w-5 h-5" />
          <span className="font-medium">Task</span>
        </button>
      </nav>
    </div>
  );
};

export default Sidebar;
