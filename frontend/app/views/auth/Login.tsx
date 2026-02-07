import { Card } from "@/components/ui/card";
import Auth from "../layouts/Auth";
import { Label } from "@/components/ui/label";
import Required from "@/components/ui/required";
import { ArrowRight, Lock, Mail } from "lucide-react";
import { Input } from "@/components/ui/input";
import { Checkbox } from "@/components/ui/checkbox";
import { Button } from "@/components/ui/button";
import { useState } from "react";
import type { UserAuth } from "app/types";
import { Link, useNavigate, type MetaFunction } from "react-router";
import { FieldError } from "@/components/ui/field";
import useLogin from "app/hooks/useLogin";

// eslint-disable-next-line react-refresh/only-export-components
export const meta: MetaFunction = () => {
  return [{ title: "Login - Project Hub" }];
};

const Login = () => {
  const navigate = useNavigate();
  const { userLogin, error } = useLogin();
  const [formData, setFormData] = useState<UserAuth>({
    email: "",
    password: "",
  });

  const inputHandler =
    (input: keyof UserAuth) => (e: React.ChangeEvent<HTMLInputElement>) => {
      setFormData((prev) => ({
        ...prev,
        [input]: e.target.value,
      }));
    };

  const formHandler = async (e: React.SubmitEvent<HTMLFormElement>) => {
    e.preventDefault();

    try {
      const data = await userLogin(formData);
      if (data.success === true) {
        navigate("/project");
      }
    } catch (error) {
      console.error(error);
    }
  };

  return (
    <Auth>
      <Card className="p-8 border-gray-200 shadow-sm">
        <form onSubmit={formHandler} className="space-y-6">
          <div className="space-y-2">
            <Label htmlFor="email" className="text-gray-900">
              Email Address <Required />
            </Label>

            <div className="relative">
              <Mail className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
              <Input
                id="email"
                type="email"
                placeholder="you@company.com"
                required
                onChange={inputHandler("email")}
                className="pl-10 border-gray-300 focus:border-black focus:ring-black"
                aria-invalid={!!error.email}
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
                placeholder="Enter your password"
                required
                onChange={inputHandler("password")}
                className="pl-10 border-gray-300 focus:border-black focus:ring-black"
                aria-invalid={!!error.password}
              />
            </div>
            {error.password && <FieldError>{error.password[0]}</FieldError>}
          </div>

          <div className="flex items-center justify-between text-gray-600">
            <label className="flex items-center gap-2 cursor-pointer">
              <Checkbox id="remember" className="border-1 border-black" />
              <span>Remember Me</span>
            </label>
            <a
              href="http://google.com"
              className="text-gray-900 hover:underline"
            >
              Forgot Password?
            </a>
          </div>

          <Button
            type="submit"
            className="w-full bg-black hover:bg-gray-800 text-white"
          >
            Sign In
            <ArrowRight className="w-4 h-4 ml-2" />
          </Button>
        </form>

        <div className="mt-6 text-center">
          <p className="text-gray-600">
            Don't have an account?{" "}
            <Link to="/" className="text-gray-900 font-medium hover:underline">
              Sign up
            </Link>
          </p>
        </div>
      </Card>
    </Auth>
  );
};

export default Login;
