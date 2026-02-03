import { Card } from "@/components/ui/card";
import Auth from "./layouts/Auth";
import { NavLink, type MetaFunction } from "react-router";
import { Button } from "@/components/ui/button";
import { useEffect } from "react";

// eslint-disable-next-line react-refresh/only-export-components
export const meta: MetaFunction = () => {
  return [{ title: "Test Page - Project Hub" }];
};

const TestPage = () => {
  useEffect(() => {
    const getProfileData = async () => {
      const response = await fetch(`${import.meta.env.VITE_APP_URL}/profile`, {
        credentials: "include",
      });

      const data = (await response.json()) as Response;
      console.log(data);
    };

    getProfileData();
  }, []);

  return (
    <Auth>
      <Card className="p-8 border-gray-200 shadow-sm">
        Test page
        <Button variant="link">
          <NavLink to="/">To Register</NavLink>
        </Button>
      </Card>
    </Auth>
  );
};

export default TestPage;
