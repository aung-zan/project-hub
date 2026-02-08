import type { MetaFunction } from "react-router";
import Home from "../layouts/Home";
import { CheckSquare } from "lucide-react";

// eslint-disable-next-line react-refresh/only-export-components
export const meta: MetaFunction = () => {
  return [{ title: "Create project - Project Hub" }];
};

const Create = () => {
  return (
    <Home>
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Header */}
        <div className="flex items-center gap-3 mb-8">
          <div className="w-12 h-12 bg-gray-900 rounded-lg flex items-center justify-center">
            <CheckSquare className="w-6 h-6 text-white" />
          </div>
          <div>
            <h1 className="text-gray-900">Create task</h1>
            <p className="text-gray-600">Create a new task.</p>
          </div>
        </div>

        {/* Body */}
        <div className="flex items-center justify-center h-[60vh]">
          <div className="text-center">
            <div className="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <CheckSquare className="w-12 h-12 text-gray-400" />
            </div>
            <h2 className="text-gray-900 mb-2">Create task Coming Soon</h2>
            <p className="text-gray-600">
              Create task content will be displayed here
            </p>
          </div>
        </div>
      </div>
    </Home>
  );
};

export default Create;
