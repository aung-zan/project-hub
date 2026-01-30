import { Card } from "@/components/ui/card";
import Auth from "./layouts/Auth";
import { NavLink } from "react-router";
import { Button } from "@/components/ui/button";

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
