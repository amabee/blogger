import React from "react";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Card } from "@/components/ui/card";
import { ArrowDown, ArrowUp, Bookmark } from "lucide-react";

const BlogCard = ({ post }) => {
  return (
    <Card
      key={post.id}
      className="p-4 hover:bg-white/50 dark:hover:bg-zinc-950/50 transition-colors bg-white dark:bg-zinc-950 shadow-md"
    >
      <div className="flex gap-4">
        <Avatar className="w-12 h-12 ring-2 ring-green-500/20">
          <AvatarImage src={post.user.avatar} />
          <AvatarFallback>{post.user.name[0]}</AvatarFallback>
        </Avatar>
        <div className="flex-1">
          <div className="flex items-center gap-2">
            <span className="font-semibold hover:text-green-500 cursor-pointer">
              {post.user.name}
            </span>
            {post.user.badge && (
              <div className="bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs px-2 py-0.5 rounded-full">
                {post.user.badge}
              </div>
            )}
            <span className="text-muted-foreground text-sm">
              {post.timestamp}
            </span>
          </div>
          <p className="mt-2 text-foreground text-base leading-relaxed">
            {post.content}
          </p>
          {post.image && (
            <div className="mt-3 rounded-xl overflow-hidden">
              <img
                src={post.image}
                alt="Post content"
                className="w-full object-cover"
              />
            </div>
          )}
          {post.hashtags && (
            <p className="mt-3 text-green-500 space-x-2 text-sm">
              {post.hashtags.map((tag) => (
                <span
                  key={tag}
                  className="hover:text-green-600 cursor-pointer hover:underline"
                >
                  {tag}
                </span>
              ))}
            </p>
          )}
          <div className="flex gap-6 mt-4">
            <button className="flex items-center gap-2 text-muted-foreground hover:text-green-500 transition-colors group">
              <ArrowUp className="h-5 w-5 group-hover:scale-110 transition-transform" />
              <span className="text-sm">{post.likes}</span>
            </button>
            <button className="flex items-center gap-2 text-muted-foreground hover:text-green-500 transition-colors group">
              <ArrowDown className="h-5 w-5 group-hover:scale-110 transition-transform" />
              <span className="text-sm">{post.comments}</span>
            </button>
            <button className="ml-auto text-muted-foreground hover:text-green-500 transition-colors group">
              <Bookmark className="h-5 w-5 group-hover:scale-110 transition-transform" />
            </button>
          </div>
        </div>
      </div>
    </Card>
  );
};

export default BlogCard;
