import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import Auth from "../layouts/Auth";

const Register = () => {
  return (
    <Auth>
      <Card className="p-8 border-gray-200 shadow-sm">
        <form action="">
          Register Page
          <Button variant="outline">Hello with Shadcn</Button>
          <Button variant="destructive">Hello with Shadcn</Button>
          <Button variant="link">Hello with Shadcn</Button>
        </form>
      </Card>
    </Auth>
  );
};

export default Register;
