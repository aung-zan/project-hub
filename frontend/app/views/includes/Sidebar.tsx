import { Label } from "@/components/ui/label";
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

      <nav className="flex-1 py-4 space-y-1 overflow-y-auto">
        <Label className="px-7 pb-2 text-gray-400">Workspace</Label>

        <Link to="/project">
          <button className="w-full flex items-center gap-3 mb-2 px-7 py-3 border-l-3 border-black bg-gray-200 cursor-pointer">
            <FolderKanban className="w-5 h-5" />
            <span className="font-medium">Project</span>
          </button>
        </Link>

        <button className="w-full flex items-center gap-3 px-7 py-3 hover:bg-gray-200 cursor-pointer">
          <CheckSquare className="w-5 h-5" />
          <span className="text-gray-600">Task</span>
        </button>
      </nav>
    </div>
  );
};

export default Sidebar;
