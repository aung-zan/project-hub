import type { PropsWithChildren } from "react";

const Auth = ({ children }: PropsWithChildren) => {
  return (
    <div className="min-h-screen bg-white flex items-center justify-center p-4">
      <div className="w-full max-w-md">
        {/* Logo/Brand */}
        <div className="text-center mb-8">
          <img
            src="./src/assets/hub.svg"
            alt="Project Hub"
            className="inline-flex items-center justify-center w-18 h-18 mb-4"
          />

          <h1 className="text-gray-900 mb-2">Project Hub</h1>
          <p className="text-gray-600">Create your account</p>
        </div>

        {children}

        {/* Footer */}
        <div className="mt-8 text-center text-gray-600">
          <p>© 2025 Project Hub. All rights reserved.</p>
        </div>
      </div>
    </div>
  );
};

export default Auth;
