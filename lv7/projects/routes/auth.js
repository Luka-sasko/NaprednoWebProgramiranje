const express = require('express');
const router = express.Router();
const User = require('../models/user'); // Promijenjeno u user
const bcrypt = require('bcryptjs');
const passport = require('passport');

router.get('/login', (req, res) => {
  res.render('login');
});

router.get('/register', (req, res) => {
  res.render('register');
});

router.post('/register', async (req, res) => {
  const hashed = await bcrypt.hash(req.body.password, 10);
  const user = new User({ username: req.body.username, password: hashed });
  await user.save();
  req.flash('success_msg', 'Uspješno ste se registrirali!');
  res.redirect('/login');
});

router.post('/login', passport.authenticate('local', {
  successRedirect: '/projects',
  failureRedirect: '/login',
  failureFlash: true
}));

router.get('/logout', (req, res) => {
  req.logout(() => res.redirect('/login'));
});

module.exports = router;