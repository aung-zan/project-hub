import rateLimit from "express-rate-limit";

const limiter = rateLimit({
  windowMs: 10 * 60 * 1000, // 10 mintues
  limit: 100, // limit after 100 requests
  standardHeaders: "draft-8",
  legacyHeaders: false,
  ipv6Subnet: 56,
  message: "Please try again after 10 minutes.",
});

export default limiter;
