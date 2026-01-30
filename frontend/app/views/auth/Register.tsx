import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import Auth from "../layouts/Auth";
import { useState } from "react";
import type { Response, UserCreate } from "app/types";
import { Input } from "@/components/ui/input";
import Required from "@/components/ui/required";
import { FieldError } from "@/components/ui/field";
import { Label } from "@/components/ui/label";
import { ArrowRight, Lock, Mail, User } from "lucide-react";
import { Checkbox } from "@/components/ui/checkbox";
import { useNavigate } from "react-router";
import { connectToServer } from "app/utils/helper";

const Register = () => {
  const naviate = useNavigate();
  const [error, setError] = useState<Record<string, string[]>>({});
  const [formData, setFormData] = useState<UserCreate>({
    name: "",
    username: "",
    email: "",
    password: "",
    confirm_password: "",
  });

  const inputHandler =
    (input: keyof UserCreate) => (e: React.ChangeEvent<HTMLInputElement>) => {
      setFormData((prev) => ({
        ...prev,
        [input]: e.target.value,
      }));
    };

  const formHandler = async (e: React.SubmitEvent) => {
    e.preventDefault();

    try {
      const response = await connectToServer({
        path: "register",
        method: "post",
        data: formData,
        token: false,
      });

      const data = (await response.json()) as Response;

      if (data.success === true) {
        naviate("login");
      }

      if (data.message && typeof data.message === "object") {
        setError(data.message);
      }
    } catch (error) {
      console.error(error);
    }
  };

  return (
    <Auth>
      {/* Register Form */}
      <Card className="p-8 border-gray-200 shadow-sm">
        <form onSubmit={formHandler} className="space-y-6">
          <div className="space-y-2">
            <Label htmlFor="name" className="text-gray-900">
              Full Name <Required />
            </Label>

            <div className="relative">
              <User className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
              <Input
                id="name"
                type="text"
                placeholder="John Doe"
                required
                onChange={inputHandler("name")}
                aria-invalid={!!error.name}
                className="pl-10 border-gray-300 focus:border-black focus:ring-black"
              />
            </div>
            {error.name && <FieldError>{error.name[0]}</FieldError>}
          </div>

          <div className="space-y-2">
            <Label htmlFor="username" className="text-gray-900">
              Username <Required />
            </Label>

            <div className="relative">
              <User className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
              <Input
                id="username"
                type="text"
                placeholder="John Doe"
                required
                onChange={inputHandler("username")}
                aria-invalid={!!error.username}
                className="pl-10 border-gray-300 focus:border-black focus:ring-black"
              />
            </div>
            {error.username && <FieldError>{error.username[0]}</FieldError>}
          </div>

          <div className="space-y-2">
            <Label htmlFor="email" className="text-gray-900">
              Email <Required />
            </Label>

            <div className="relative">
              <Mail className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
              <Input
                id="email"
                type="email"
                placeholder="John Doe"
                required
                onChange={inputHandler("email")}
                aria-invalid={!!error.email}
                className="pl-10 border-gray-300 focus:border-black focus:ring-black"
              />
            </div>
            {error.email && <FieldError>{error.email[0]}</FieldError>}
          </div>

          <div className="space-y-2">
            <Label htmlFor="password" className="text-gray-900">
              Password <Required />
            </Label>

            <div className="relative">
              <Lock className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
              <Input
                id="password"
                type="password"
                placeholder="John Doe"
                required
                onChange={inputHandler("password")}
                aria-invalid={!!error.password}
                className="pl-10 border-gray-300 focus:border-black focus:ring-black"
              />
            </div>
            {error.password && <FieldError>{error.password[0]}</FieldError>}
          </div>

          <div className="space-y-2">
            <Label htmlFor="confirm_password" className="text-gray-900">
              Confirm Password <Required />
            </Label>

            <div className="relative">
              <Lock className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
              <Input
                id="confirm_password"
                type="password"
                placeholder="John Doe"
                required
                onChange={inputHandler("confirm_password")}
                aria-invalid={!!error.confirm_password}
                className="pl-10 border-gray-300 focus:border-black focus:ring-black"
              />
            </div>
            {error.confirm_password && (
              <FieldError>{error.confirm_password[0]}</FieldError>
            )}
          </div>

          <div className="flex items-start gap-2">
            <Checkbox id="terms" className="mt-1" required />
            <label htmlFor="terms" className="text-gray-600">
              I agree to the{" "}
              <button type="button" className="text-gray-900 hover:underline">
                Terms of Service
              </button>{" "}
              and{" "}
              <button type="button" className="text-gray-900 hover:underline">
                Privacy Policy
              </button>
            </label>
          </div>

          <Button
            type="submit"
            className="w-full bg-black hover:bg-gray-800 text-white"
          >
            Create Account
            <ArrowRight className="w-4 h-4 ml-2" />
          </Button>
        </form>

        <div className="mt-6 text-center">
          <p className="text-gray-600">
            Already have an account?{" "}
            <button className="text-gray-900 hover:underline">Sign in</button>
          </p>
        </div>
      </Card>
    </Auth>
  );
};

export default Register;
