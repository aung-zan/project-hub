import { Card } from "@/components/ui/card";
import Auth from "./layouts/Auth";

const TestPage = () => {
  return (
    <Auth>
      <Card className="p-8 border-gray-200 shadow-sm">Test page</Card>
    </Auth>
  );
};

export default TestPage;
