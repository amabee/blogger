"use client";
import React from "react";
import { useState } from "react";
import {
  Card,
  CardHeader,
  CardTitle,
  CardDescription,
  CardContent,
  CardFooter,
} from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { AlertCircle, Moon, Sun, X } from "lucide-react";
import { Alert, AlertDescription } from "@/components/ui/alert";
import { useTheme } from "next-themes";
import { BorderBeam } from "@/components/ui/border-beam";

import { Bounce, ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

import ThemeComponent from "@/components/shared_component/ThemeComponent";
import { login } from "@/lib/lib";

const LoginPage = () => {
  const notify = () => toast("Wow so easy!");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const { theme, setTheme } = useTheme();

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    setLoading(true);

    if (!email || !password) {
      setError("Please fill in all fields");
      setLoading(false);
      return;
    }

    try {
      const { success, message } = await login(email, password);

      if (success === false) {
        toast(message, {
          position: "top-center",
          autoClose: 3000,
          hideProgressBar: false,
          closeOnClick: false,
          rtl: false,
          pauseOnFocusLoss: false,
          draggable: true,
          pauseOnHover: true,
          theme: theme === "dark" ? "dark" : "light",
          transition: Bounce,
          icon: <X color="red" />,
        });
        return;
      }

      return window.location.href = "/";


    } catch (err) {
      setError(`Exception caught: ${err}`);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-background p-4 sm:p-6 md:p-8 overflow-hidden relative">
      <ThemeComponent theme={theme} />
      <Button
        variant="ghost"
        size="icon"
        className="fixed top-4 right-4 sm:absolute md:top-6 md:right-6"
        onClick={() => setTheme(theme === "dark" ? "light" : "dark")}
      >
        <Sun className="h-[18px] w-[18px] sm:h-5 sm:w-5 rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
        <Moon className="absolute h-[18px] w-[18px] sm:h-5 sm:w-5 rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
        <span className="sr-only">Toggle theme</span>
      </Button>

      <Card className="w-full max-w-[320px] sm:max-w-[380px] md:max-w-md relative">
        <CardHeader className="space-y-1 px-4 sm:px-6">
          <CardTitle className="text-xl sm:text-2xl font-bold text-center">
            Welcome Back
          </CardTitle>
          <CardDescription className="text-center text-sm sm:text-base">
            Enter your credentials to access your blog
          </CardDescription>
        </CardHeader>
        <CardContent className="px-4 sm:px-6">
          <form
            onSubmit={handleSubmit}
            method="POST"
            className="space-y-3 sm:space-y-4"
          >
            {error && (
              <Alert variant="destructive">
                <AlertCircle className="h-4 w-4" />
                <AlertDescription className="text-sm">{error}</AlertDescription>
              </Alert>
            )}
            <div className="space-y-1 sm:space-y-2">
              <Label htmlFor="email" className="text-sm sm:text-base">
                Email
              </Label>
              <Input
                id="email"
                type="email"
                placeholder="name@example.com"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
                className="h-9 sm:h-10 text-sm sm:text-base"
              />
            </div>
            <div className="space-y-1 sm:space-y-2">
              <Label htmlFor="password" className="text-sm sm:text-base">
                Password
              </Label>
              <Input
                id="password"
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
                className="h-9 sm:h-10 text-sm sm:text-base"
              />
            </div>
            <Button
              type="submit"
              className="w-full h-9 sm:h-10 text-sm sm:text-base"
              disabled={loading}
            >
              {loading ? "Signing in..." : "Sign In"}
            </Button>
          </form>
        </CardContent>
        <CardFooter className="flex flex-col space-y-2 px-4 sm:px-6">
          <div className="text-xs sm:text-sm text-center text-muted-foreground">
            Don't have an account?{" "}
            <a
              href="/authentication/signup"
              className="text-primary hover:underline"
            >
              Sign up
            </a>
          </div>
          <a
            href="/forgot-password"
            className="text-xs sm:text-sm text-center text-primary hover:underline"
          >
            Forgot your password?
          </a>
        </CardFooter>
        <BorderBeam size={250} duration={12} delay={9} />
      </Card>
      <ToastContainer />
    </div>
  );
};

export default LoginPage;
