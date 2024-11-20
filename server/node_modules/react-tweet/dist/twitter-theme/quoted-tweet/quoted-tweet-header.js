import { jsx as _jsx, jsxs as _jsxs } from "react/jsx-runtime";
import clsx from 'clsx';
import { AvatarImg } from '../avatar-img.js';
import s from './quoted-tweet-header.module.css';
import { VerifiedBadge } from '../verified-badge.js';
export const QuotedTweetHeader = ({ tweet })=>{
    const { user } = tweet;
    return /*#__PURE__*/ _jsxs("div", {
        className: s.header,
        children: [
            /*#__PURE__*/ _jsx("a", {
                href: tweet.url,
                className: s.avatar,
                target: "_blank",
                rel: "noopener noreferrer",
                children: /*#__PURE__*/ _jsx("div", {
                    className: clsx(s.avatarOverflow, user.profile_image_shape === 'Square' && s.avatarSquare),
                    children: /*#__PURE__*/ _jsx(AvatarImg, {
                        src: user.profile_image_url_https,
                        alt: user.name,
                        width: 20,
                        height: 20
                    })
                })
            }),
            /*#__PURE__*/ _jsxs("div", {
                className: s.author,
                children: [
                    /*#__PURE__*/ _jsx("div", {
                        className: s.authorText,
                        children: /*#__PURE__*/ _jsx("span", {
                            title: user.name,
                            children: user.name
                        })
                    }),
                    /*#__PURE__*/ _jsx(VerifiedBadge, {
                        user: user
                    }),
                    /*#__PURE__*/ _jsx("div", {
                        className: s.username,
                        children: /*#__PURE__*/ _jsxs("span", {
                            title: `@${user.screen_name}`,
                            children: [
                                "@",
                                user.screen_name
                            ]
                        })
                    })
                ]
            })
        ]
    });
};
