import type { PropsWithChildren } from "react";
import Sidebar from "../includes/Sidebar";
import Navbar from "../includes/Navbar";

const Home = ({ children }: PropsWithChildren) => {
  return (
    <div id="container">
      <div className="tailwind">
        <div className="h-full">
          <div className="flex h-screen bg-white overflow-hidden">
            <Sidebar />
            <div className="flex-1 flex flex-col h-full overflow-hidden">
              <Navbar />
              <main className="flex-1 overflow-auto bg-gray-50/30">
                <div className="min-h-screen bg-white">{children}</div>
              </main>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Home;
