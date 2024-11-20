"use client";
import React from "react";
import { useState, useEffect } from "react";
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
import {
  AlertCircle,
  Moon,
  Sun,
  Eye,
  EyeOff,
  CalendarIcon,
  X,
  Check,
} from "lucide-react";
import { Alert, AlertDescription } from "@/components/ui/alert";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";

import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover";
import { format } from "date-fns";
import { useTheme } from "next-themes";
import { BorderBeam } from "@/components/ui/border-beam";
import ThemeComponent from "@/components/shared_component/ThemeComponent";
import { useDebounce } from "@uidotdev/usehooks";
import { checkUsernameAvailability, signup } from "@/lib/auth/auth";
import { Calendar } from "@/components/ui/calendar";
import { Bounce, ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

const RegistrationPage = () => {
  const [formData, setFormData] = useState({
    firstname: "",
    lastname: "",
    username: "",
    email: "",
    password: "",
    confirmPassword: "",
    dob: "",
    phone_num: "",
    gender: "",
  });
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");
  const [loading, setLoading] = useState(false);
  const [showPassword, setShowPassword] = useState(false);
  const [isSearching, setIsSearching] = useState(false);
  const { theme, setTheme } = useTheme();
  const [date, setDate] = useState();

  const debouncedUsername = useDebounce(formData.username, 300);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
    setError("");
    setSuccess("");
  };

  const handleDateChange = (newDate) => {
    const currentYear = new Date().getFullYear();
    const selectedYear = newDate.getFullYear();

    if (selectedYear === currentYear) {
      setError("Cannot select a date from the current year");
      return;
    }

    setDate(newDate);
    setFormData((prev) => ({
      ...prev,
      dob: format(newDate, "yyyy-MM-dd"),
    }));
    setError("");
  };

  const handleGenderChange = (value) => {
    setFormData((prev) => ({
      ...prev,
      gender: value,
    }));
    setError("");
  };

  const validateForm = () => {
    if (Object.values(formData).some((value) => !value.trim())) {
      setError("Please fill in all fields");
      return false;
    }

    if (formData.password.length < 8) {
      setError("Password must be at least 8 characters long");
      return false;
    }

    if (formData.password !== formData.confirmPassword) {
      setError("Passwords do not match");
      return false;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(formData.email)) {
      setError("Please enter a valid email address");
      return false;
    }

    const phoneRegex = /^\+?[\d\s-]{10,}$/;
    if (!phoneRegex.test(formData.phone_num)) {
      setError("Please enter a valid phone number");
      return false;
    }

    if (!formData.dob) {
      setError("Please select your date of birth");
      return false;
    }

    return true;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    setSuccess("");

    if (!validateForm()) return;

    setLoading(true);

    try {
      const { success, message } = await signup(
        formData.firstname,
        formData.lastname,
        formData.username,
        formData.email,
        formData.confirmPassword,
        formData.dob,
        formData.phone_num,
        formData.gender
      );

      if (!success) {
        setError(message);
        console.log(message);
        return;
      }

      toast("Registration Successful", {
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
        icon: <Check color="green" />,
      });

      setFormData({
        firstname: "",
        lastname: "",
        username: "",
        email: "",
        password: "",
        confirmPassword: "",
        dob: "",
        phone_num: "",
        gender: "",
      });
    } catch (err) {
      setError(err.message || "Failed to register. Please try again.");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    const checkUsername = async () => {
      setIsSearching(true);
      setError("");
      setSuccess("");

      if (debouncedUsername.length < 3) {
        setIsSearching(false);
        return;
      }

      try {
        const { success, message } = await checkUsernameAvailability(
          debouncedUsername
        );

        console.log(message);
        if (message === true) {
          setError("Username already taken");
        } else {
          setSuccess("Username available");
        }
      } catch (err) {
        console.log(err);
        setError("Error checking username availability");
      } finally {
        setIsSearching(false);
      }
    };

    if (debouncedUsername) {
      checkUsername();
    }
  }, [debouncedUsername]);

  const renderPasswordInput = (id, label) => (
    <div className="space-y-2">
      <Label htmlFor={id}>{label}</Label>
      <div className="relative">
        <Input
          id={id}
          name={id}
          type={showPassword ? "text" : "password"}
          value={formData[id]}
          onChange={handleChange}
          required
          className="pr-10"
        />
        {id === "password" && (
          <Button
            type="button"
            variant="ghost"
            size="icon"
            className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
            onClick={() => setShowPassword(!showPassword)}
          >
            {showPassword ? (
              <EyeOff className="h-4 w-4" />
            ) : (
              <Eye className="h-4 w-4" />
            )}
          </Button>
        )}
      </div>
    </div>
  );

  return (
    <div className="min-h-screen flex items-center justify-center bg-background p-4 sm:p-6 md:p-8 overflow-hidden relative">
      <ToastContainer />
      <ThemeComponent theme={theme} />
      <Button
        variant="ghost"
        size="icon"
        className="absolute top-4 right-4"
        onClick={() => setTheme(theme === "dark" ? "light" : "dark")}
      >
        <Sun className="h-5 w-5 rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
        <Moon className="absolute h-5 w-5 rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
        <span className="sr-only">Toggle theme</span>
      </Button>

      <Card className="w-full max-w-md relative">
        <CardHeader className="space-y-1">
          <CardTitle className="text-2xl font-bold text-center">
            Create an Account
          </CardTitle>
          <CardDescription className="text-center">
            Enter your details to register for the blog
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit} className="space-y-4">
            {error && (
              <Alert variant="destructive">
                <AlertCircle className="h-4 w-4 " />
                <AlertDescription>{error}</AlertDescription>
              </Alert>
            )}
            {success && (
              <Alert variant="default" className="text-green-600">
                <AlertCircle className="h-4 w-4" />
                <AlertDescription>{success}</AlertDescription>
              </Alert>
            )}

            {["firstname", "lastname", "username", "email"].map((field) => (
              <div key={field} className="space-y-2">
                <Label htmlFor={field} className="capitalize">
                  {field === "firstname"
                    ? "First Name"
                    : field === "lastname"
                    ? "Last Name"
                    : field}
                </Label>
                <Input
                  id={field}
                  name={field}
                  type={field === "email" ? "email" : "text"}
                  placeholder={field === "email" ? "name@example.com" : ""}
                  value={formData[field]}
                  onChange={handleChange}
                  required
                  disabled={loading}
                />
              </div>
            ))}

            {renderPasswordInput("password", "Password")}
            {renderPasswordInput("confirmPassword", "Confirm Password")}

            <div className="space-y-2">
              <Label>Date of Birth</Label>
              <Popover>
                <PopoverTrigger asChild>
                  <Button
                    variant="outline"
                    className={`w-full justify-start text-left font-normal ${
                      !date && "text-muted-foreground"
                    }`}
                  >
                    <CalendarIcon className="mr-2 h-4 w-4" />
                    {date ? format(date, "PPP") : "Pick a date"}
                  </Button>
                </PopoverTrigger>
                <PopoverContent className="w-auto p-0" align="start">
                  <Calendar
                    mode="single"
                    selected={date}
                    onSelect={handleDateChange}
                    disabled={(date) => {
                      const currentYear = new Date().getFullYear();
                      return date.getFullYear() === currentYear;
                    }}
                    initialFocus
                  />
                </PopoverContent>
              </Popover>
            </div>

            <div className="space-y-2">
              <Label htmlFor="phone_num">Phone Number</Label>
              <Input
                id="phone_num"
                name="phone_num"
                type="tel"
                placeholder="+1234567890"
                value={formData.phone_num}
                onChange={handleChange}
                required
                disabled={loading}
              />
            </div>

            <div className="space-y-2">
              <Label>Gender</Label>
              <Select
                value={formData.gender}
                onValueChange={handleGenderChange}
                disabled={loading}
              >
                <SelectTrigger>
                  <SelectValue placeholder="Select gender" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="male">Male</SelectItem>
                  <SelectItem value="female">Female</SelectItem>
                  <SelectItem value="other">Other</SelectItem>
                  <SelectItem value="prefer_not_to_say">
                    Prefer not to say
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>

            <Button
              type="submit"
              className="w-full"
              disabled={loading || isSearching}
            >
              {loading ? "Creating Account..." : "Create Account"}
            </Button>
          </form>
        </CardContent>
        <CardFooter className="flex flex-col space-y-2">
          <div className="text-sm text-center text-muted-foreground">
            Already have an account?{" "}
            <a
              href="/authentication/login"
              className="text-primary hover:underline"
            >
              Sign in
            </a>
          </div>
        </CardFooter>
        <BorderBeam size={250} duration={12} delay={9} />
      </Card>
    </div>
  );
};

export default RegistrationPage;
