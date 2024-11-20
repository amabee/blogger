import React from "react";
import { Card } from "@/components/ui/card";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { Mail, MapPin, Link2, Calendar } from "lucide-react";

const ProfileCard = () => {
  return (
    <Card className="overflow-hidden w-full bg-white dark:bg-zinc-950 shadow-md">
      {/* Cover Image with Gradient Overlay */}
      <div className="h-36 bg-gradient-to-br from-green-400 via-green-500 to-emerald-600 relative">
        <div className="absolute inset-0 bg-black/10"></div>
      </div>

      {/* Profile Content */}
      <div className="px-4 pb-4">
        {/* Avatar and Edit Button */}
        <div className="relative -mt-16 mb-4 flex justify-between items-start">
          <Avatar className="w-28 h-28 border-4 border-background shadow-xl">
            <AvatarImage src="/api/placeholder/112/112" />
            <AvatarFallback>JD</AvatarFallback>
          </Avatar>
          <Button className="bg-green-500 hover:bg-green-600 text-white shadow-md mt-20">
            Edit Profile
          </Button>
        </div>

        {/* User Info */}
        <div className="space-y-1">
          <div className="flex items-center gap-2">
            <h2 className="text-2xl font-bold">John Doe</h2>
            <div className="bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs px-2 py-1 rounded-full">
              Pro
            </div>
          </div>
          <p className="text-muted-foreground text-sm">@johndoe</p>
          <p className="text-foreground mt-3 text-sm leading-relaxed">
            Full-stack developer passionate about building beautiful web
            experiences. Open source contributor. Coffee enthusiast ☕️
          </p>
        </div>

        {/* Stats Grid */}
        <div className="grid grid-cols-4 gap-4 mt-6 mb-6">
          {[
            { label: "Following", value: "234" },
            { label: "Followers", value: "1.2k" },
            { label: "Posts", value: "89" },
            { label: "Likes", value: "2.1k" },
          ].map((stat) => (
            <button
              key={stat.label}
              className="flex flex-col items-center p-2 rounded-xl hover:bg-muted/80 transition-colors"
            >
              <span className="font-bold text-foreground">{stat.value}</span>
              <span className="text-xs text-muted-foreground">
                {stat.label}
              </span>
            </button>
          ))}
        </div>

        {/* Additional Info */}
        <div className="grid grid-cols-2 gap-4 text-sm">
          <div className="flex items-center gap-2 text-muted-foreground">
            <MapPin className="h-4 w-4 text-green-500" />
            <span>San Francisco, CA</span>
          </div>
          <div className="flex items-center gap-2 text-muted-foreground">
            <Calendar className="h-4 w-4 text-green-500" />
            <span>Joined Dec 2023</span>
          </div>
          <div className="flex items-center gap-2">
            <Link2 className="h-4 w-4 text-green-500" />
            <a
              href="#"
              className="text-green-500 hover:text-green-600 hover:underline"
            >
              github.com/johndoe
            </a>
          </div>
          <div className="flex items-center gap-2">
            <Mail className="h-4 w-4 text-green-500" />
            <a
              href="#"
              className="text-green-500 hover:text-green-600 hover:underline"
            >
              Email
            </a>
          </div>
        </div>
      </div>
    </Card>
  );
};

export default ProfileCard;
