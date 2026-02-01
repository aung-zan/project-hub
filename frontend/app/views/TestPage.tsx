import { Card } from "@/components/ui/card";
import Auth from "./layouts/Auth";
import { NavLink, type MetaFunction } from "react-router";
import { Button } from "@/components/ui/button";

// eslint-disable-next-line react-refresh/only-export-components
export const meta: MetaFunction = () => {
  return [{ title: "Test Page - Project Hub" }];
};

const TestPage = () => {
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
