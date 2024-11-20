"use client";
import { useEffect, useState } from "react";
import Ripple from "@/components/ui/ripple";
import FlickeringGrid from "@/components/ui/flickering-grid";
import Meteors from "@/components/ui/meteors";

const ThemeComponent = ({ theme: initialTheme, onChange }) => {
  const [mounted, setMounted] = useState(false);
  const [theme, setTheme] = useState(initialTheme);

  useEffect(() => {
    setMounted(true);
  }, []);

  // Handle theme changes from parent
  useEffect(() => {
    setTheme(initialTheme);
    // Call onChange if provided and theme changed
    if (onChange && mounted) {
      onChange(initialTheme);
    }
  }, [initialTheme, onChange, mounted]);

  if (!mounted) {
    return null;
  }

  return (
    <div>
      {theme === "dark" ? (
        <Meteors number={30} />
      ) : (
        <FlickeringGrid
          className="z-0 absolute inset-0 size-full"
          squareSize={5}
          gridGap={6}
          color="#3676a3"
          maxOpacity={0.5}
          flickerChance={0.1}
        />
      )}
    </div>
  );
};

export default ThemeComponent;
