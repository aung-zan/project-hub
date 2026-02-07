import { Link, type MetaFunction } from "react-router";
import Home from "../layouts/Home";
import { FolderKanban, Plus } from "lucide-react";
import { Button } from "@/components/ui/button";

// eslint-disable-next-line react-refresh/only-export-components
export const meta: MetaFunction = () => {
  return [{ title: "Project - Project Hub" }];
};

const Index = () => {
  return (
    <Home>
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Header */}
        <div className="flex items-center justify-between mb-8">
          <div className="flex items-center gap-3">
            <div className="w-12 h-12 bg-gray-900 rounded-lg flex items-center justify-center">
              <FolderKanban className="w-6 h-6 text-white" />
            </div>
            <div>
              <h1 className="text-gray-900">Projects</h1>
              <p className="text-gray-600">
                Manage and track all your projects.
              </p>
            </div>
          </div>

          <Link to="/project/create">
            <Button className="bg-gray-900 hover:bg-gray-800 text-white">
              <Plus className="w-4 h-4 mr-2" />
              Create Project
            </Button>
          </Link>
        </div>

        {/* Body */}
        <div className="flex items-center justify-center h-[60vh]">
          <div className="text-center">
            <div className="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <FolderKanban className="w-12 h-12 text-gray-400" />
            </div>
            <h2 className="text-gray-900 mb-2">Projects Coming Soon</h2>
            <p className="text-gray-600">
              Projects content will be displayed here
            </p>
          </div>
        </div>
      </div>
    </Home>
  );
};

export default Index;
