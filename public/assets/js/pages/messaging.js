/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/*!*****************************************!*\
  !*** ./resources/js/pages/messaging.js ***!
  \*****************************************/


function setMobileChatHeight() {
  var chat = $('.chat-conversation');
  var chatInner = $('.chat-conversation [data-simplebar]');
  var chatPaddingY = chat.outerHeight() - chat.height();
  var chatSiblingsHeight = 0;
  chat.siblings().each(function (index) {
    chatSiblingsHeight += $(this).outerHeight();
  });
  var chatHeight = $(window).height() - chatSiblingsHeight - chatPaddingY;
  chatInner.css({
    'max-height': chatHeight
  });
}

function openMobileChat() {
  $('.chat-list-item').click(function () {
    console.log('click');
    $('.user-chat').addClass('opened');
  });
}

function closeMobileChat() {
  $('.user-chat-close').click(function () {
    $('.user-chat').removeClass('opened');
  });
}

$(function () {
  closeMobileChat();

  if ($(window).width() < 768) {
    setMobileChatHeight();
    openMobileChat();
  }

  $(window).resize(function () {
    if ($(window).width() < 768) {
      setMobileChatHeight();
      openMobileChat();
    }
  });
});
/******/ })()
;