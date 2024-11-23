"use client";

import React, { useState } from "react";
import {
  Search,
  Home,
  Bell,
  MessageSquare,
  Bookmark,
  Heart,
  MessageCircle,
  Share2,
  Send,
  Image as ImageIcon,
  Users,
  Smile,
  TrendingUp,
  User,
} from "lucide-react";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Card, CardContent } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";
import { Tabs, TabsList, TabsTrigger, TabsContent } from "@/components/ui/tabs";
import ProfileCard from "@/components/shared_component/ProfileCard";
import BlogCard from "@/components/shared_component/BlogCard";
import { cn } from "@/lib/utils";
import { Dock, DockIcon } from "@/components/ui/dock";
import { Label } from "@/components/ui/label";

const Navigation = () => {
  const [activeIcon, setActiveIcon] = useState("home");

  const navItems = [
    { id: "home", icon: Home, label: "Home" },
    { id: "notifications", icon: Bell, label: "Notifications" },
    { id: "messages", icon: MessageSquare, label: "Messages" },
    { id: "search", icon: Search, label: "Search" },
  ];

  return (
    <div className="fixed bottom-4 left-1/2 -translate-x-1/2 z-50">
      <Dock magnification={60} distance={100}>
        {navItems.map(({ id, icon: Icon, label }) => (
          <DockIcon
            key={id}
            onClick={() => setActiveIcon(id)}
            className="bg-green-800"
          >
            <Icon
              className={cn(
                " transition-all duration-200  rounded-full",
                activeIcon === id ? "scale-100" : "scale-70"
              )}
            />
          </DockIcon>
        ))}
      </Dock>
    </div>
  );
};

const BugbookLayout = () => {
  const [postContent, setPostContent] = useState("");

  const posts = [
    {
      id: 1,
      user: {
        name: "Coding in Flow",
        avatar: "/api/placeholder/40/40",
        badge: "Pro",
      },
      content: "Subscribe to Coding in Flow if you haven't yet!",
      timestamp: "21 hours ago",
      likes: 142,
      comments: 23,
      shares: 7,
      bookmarks: 12,
      image: "/api/placeholder/400/300",
    },
    {
      id: 2,
      user: {
        name: "Coding in Flow",
        avatar: "/api/placeholder/40/40",
        badge: "Pro",
      },
      content: "A variable is a box where you can put a value in!",
      timestamp: "21 hours ago",
      likes: 89,
      comments: 15,
      shares: 4,
      bookmarks: 8,
      hashtags: ["#learntocode", "#programming"],
    },
  ];

  const trendingTopics = [
    { tag: "#codinginflow", posts: 2342, trend: "+12%" },
    { tag: "#typescript", posts: 1893, trend: "+8%" },
    { tag: "#webdev", posts: 1654, trend: "+15%" },
    { tag: "#javascript", posts: 1432, trend: "+5%" },
    { tag: "#react", posts: 1223, trend: "+10%" },
    { tag: "#react", posts: 1223, trend: "+10%" },
    { tag: "#react", posts: 1223, trend: "+10%" },
    { tag: "#react", posts: 1223, trend: "+10%" },
  ];

  return (
    <div className="flex flex-col min-h-screen bg-gray-50 dark:bg-zinc-900">
      {/* Fixed Header */}
      <header className="fixed top-0 w-full border-b backdrop-blur-lg bg-white/80 dark:bg-zinc-950/80 z-50">
        <div className="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
          <div className="flex items-center gap-4">
            <div className="text-green-500 font-bold text-2xl">BlogBook</div>
          </div>

          <div className="hidden md:block flex-1 max-w-md mx-4">
            <div className="relative">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
              <Input
                className="pl-9 bg-muted/50 border-none rounded-full"
                placeholder="Search"
              />
            </div>
          </div>
          <Avatar className="ring-2 ring-green-500 ring-offset-2 ring-offset-background transition-all hover:ring-green-600 cursor-pointer">
            <AvatarImage src="/api/placeholder/40/40" />
            <AvatarFallback>U</AvatarFallback>
          </Avatar>
        </div>
      </header>

      {/* Main Content Area */}
      <div className="flex-1 pt-16 pb-16 md:pb-0">
        <div className="max-w-full mx-auto px-4">
          <div className="grid grid-cols-1 md:grid-cols-12 gap-6">
            {/* Left Sidebar */}
            <div className="hidden md:block md:col-span-3">
              <div className="sticky top-20 max-h-[calc(100vh-5rem)]">
                <ProfileCard />
              </div>
            </div>

            {/* Main Content */}
            <main className="md:col-span-6 mt-4">
              <div className="space-y-6">
                {/* Post Creation Card */}
                <Card className="p-4 shadow-md bg-white dark:bg-zinc-950">
                  <div className="flex gap-4">
                    <Avatar className="w-10 h-10">
                      <AvatarImage src="/api/placeholder/40/40" />
                      <AvatarFallback>U</AvatarFallback>
                    </Avatar>
                    <div className="flex-1">
                      <textarea
                        className="w-full p-2 bg-transparent border-none focus:ring-0 resize-none text-foreground text-lg placeholder:text-muted-foreground/60"
                        placeholder="What's on your mind?"
                        rows={3}
                        value={postContent}
                        onChange={(e) => setPostContent(e.target.value)}
                      />
                      <Separator className="my-3" />
                      <div className="flex items-center justify-between mt-2">
                        <div className="flex gap-2">
                          <Button
                            variant="ghost"
                            size="sm"
                            className="text-green-500 hover:text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20"
                          >
                            <ImageIcon className="h-5 w-5" />
                          </Button>
                          <Button
                            variant="ghost"
                            size="sm"
                            className="text-green-500 hover:text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20"
                          >
                            <Smile className="h-5 w-5" />
                          </Button>
                        </div>
                        <Button
                          className="bg-green-500 hover:bg-green-600 text-white gap-2 px-6"
                          disabled={!postContent.trim()}
                        >
                          <Send className="h-4 w-4" /> Post
                        </Button>
                      </div>
                    </div>
                  </div>
                </Card>

                {/* Feed Tabs */}
                <Tabs defaultValue="for-you" className="mb-6">
                  <TabsList className="w-full bg-white dark:bg-zinc-950 p-1 shadow-md rounded-lg">
                    <TabsTrigger
                      value="for-you"
                      className="flex-1 data-[state=active]:bg-green-500 data-[state=active]:text-white rounded-md transition-all"
                    >
                      For you
                    </TabsTrigger>
                    <TabsTrigger
                      value="following"
                      className="flex-1 data-[state=active]:bg-green-500 data-[state=active]:text-white rounded-md transition-all"
                    >
                      Following
                    </TabsTrigger>
                  </TabsList>
                </Tabs>

                {/* Posts */}
                <div className="space-y-4">
                  {posts.map((post) => (
                    <BlogCard post={post} key={post.id} />
                  ))}
                </div>
              </div>
            </main>

            {/* Right Sidebar */}
            <div className="sticky top-20 max-h-[calc(100vh-8rem)] col-span-3">
              <Card
                className="p-4 shadow-md bg-white dark:bg-zinc-950 max-h-96 overflow-y-auto 
                [&::-webkit-scrollbar]:w-2
                [&::-webkit-scrollbar-track]:rounded-full
                [&::-webkit-scrollbar-track]:bg-gray-100
                [&::-webkit-scrollbar-thumb]:rounded-full
                [&::-webkit-scrollbar-thumb]:bg-gray-300
                dark:[&::-webkit-scrollbar-track]:bg-neutral-700
                dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500"
              >
                <h2 className="font-semibold text-lg mb-4 flex items-center gap-2">
                  <TrendingUp className="h-5 w-5 text-green-500" />
                  Trending topics
                </h2>
                <CardContent className="overflow-y-auto max-h-[calc(100%-3rem)]">
                  <div className="space-y-3">
                    {trendingTopics.map((topic, index) => (
                      <div
                        key={index}
                        className="p-2 rounded-lg hover:bg-muted/50 transition-colors cursor-pointer group"
                      >
                        <div className="flex justify-between items-center">
                          <div className="text-green-500 group-hover:text-green-600 font-medium">
                            {topic.tag}
                          </div>
                          <div className="text-xs text-green-500 bg-green-100 dark:bg-green-900/30 px-2 py-1 rounded-full">
                            {topic.trend}
                          </div>
                        </div>
                        <div className="text-sm text-muted-foreground mt-1">
                          {topic.posts.toLocaleString()} posts
                        </div>
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>
            </div>
          </div>
        </div>
      </div>

      {/* Mobile Navigation */}
      <div className="block ">
        <Navigation />
      </div>
    </div>
  );
};

export default BugbookLayout;
