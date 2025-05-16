const express = require("express");
const nodemailer = require("nodemailer");
const cors = require("cors");
const bodyParser = require("body-parser");
require("dotenv").config();

const app = express();

// Middleware
app.use(cors());
app.use(bodyParser.json());

// Contact form submission route
app.post("/contact", async (req, res) => {
  const { name, email, mobile, message } = req.body;

  // Validate data
  if (!name || !email || !mobile || !message) {
    return res.status(400).json({ error: "All fields are required" });
  }

  try {
    // Setup Nodemailer transporter
    let transporter = nodemailer.createTransport({
      service: "gmail",
      auth: {
        user: process.env.EMAIL_USER,
        pass: process.env.EMAIL_PASS,
      },
    });

    // Email options
    let mailOptions = {
      from: email,
      to: process.env.EMAIL_USER,
      subject: `New Contact Request from ${name}`,
      text: `Name: ${name}\nEmail: ${email}\nMobile: ${mobile}\nMessage: ${message}`,
    };

    // Send email
    await transporter.sendMail(mailOptions);
    res.status(200).json({ message: "Message sent successfully!" });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Failed to send message. Please try again later." });
  }
});

// Start server
const PORT = process.env.PORT || 5000;
app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});
